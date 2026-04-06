<?php
include 'conexao.php'; // Inclui a conexão

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // 1. Verifica se os campos estão preenchidos
    if (!empty($nome) && !empty($email) && !empty($senha)) {
        
        // 2. Verifica se o e-mail já existe no banco
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensagem = "Este e-mail já está cadastrado!";
        } else {
            // 3. Criptografa a senha (Segurança)
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            // 4. Insere no banco
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nome, $email, $senha_hash);

            if ($stmt->execute()) {
                // Sucesso! Redireciona
                header("Location: indexPublico.php");
                exit();
            } else {
                $mensagem = "Erro ao cadastrar: " . $conn->error;
            }
        }
        $stmt->close();
    } else {
        $mensagem = "Preencha todos os campos!";
    }
}
?>

<?php
// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pega os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verifica se os campos não estão vazios
    if (!empty($nome) && !empty($email) && !empty($senha)) {
        // AQUI: Você conectaria ao banco de dados para salvar
        
        // Redireciona para a página pública
        header("Location: indexPublico.php");
        exit();
    } else {
        $erro = "Por favor, preencha todos os campos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: Arial, sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background: linear-gradient(135deg, #c62828, #ff6b6b);
            padding: 20px;
        }
        
        form { 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.3); 
            width: 100%;
            max-width: 350px; 
            border-top: 5px solid #b71c1c;
        }
        
        h2 { 
            text-align: center; 
            color: #c62828; 
            margin-bottom: 20px;
            font-size: 1.5em;
        }
        
        input { 
            display: block; 
            width: 100%; 
            margin: 8px 0; 
            padding: 12px; 
            box-sizing: border-box; 
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        input:focus {
            outline: none;
            border-color: #c62828;
            box-shadow: 0 0 5px rgba(198, 40, 40, 0.3);
        }
        
        button { 
            width: 100%; 
            padding: 14px; 
            background: linear-gradient(135deg, #c62828, #e53935); 
            color: white; 
            border: none; 
            cursor: pointer; 
            margin-top: 15px; 
            font-weight: bold; 
            border-radius: 5px;
            font-size: 16px;
            transition: 0.3s;
        }
        
        button:hover { 
            background: linear-gradient(135deg, #b71c1c, #c62828);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(198, 40, 40, 0.4);
        }
        
        .voltar { 
            display: block; 
            margin-top: 15px; 
            text-align: center; 
            color: #666; 
            text-decoration: none; 
            font-size: 0.9em; 
        }
        
        .voltar:hover { 
            text-decoration: underline; 
            color: #c62828;
        }
        
        .erro { 
            color: #c62828; 
            text-align: center; 
            font-size: 0.9em; 
            margin-bottom: 10px; 
            background: #ffebee;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ffcdd2;
        }
        
        /* Seção de Login */
        .login-section {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px dashed #ffcdd2;
            text-align: center;
        }
        
        .login-section p {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 10px;
        }
        
        .btn-login {
            display: inline-block;
            width: 100%;
            padding: 12px;
            background: white;
            color: #c62828;
            border: 2px solid #c62828;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            font-size: 14px;
        }
        
        .btn-login:hover {
            background: #c62828;
            color: white;
        }
    </style>
</head>
<body>

    <form method="POST" action="">
        <h2>👤 Cadastro Usuário</h2>
        
        <?php if(isset($erro)) { echo "<p class='erro'>$erro</p>"; } ?>

        <input type="text" name="nome" placeholder="Nome Completo" required>
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="senha" placeholder="Senha" required>

        <button type="submit">Cadastrar</button>
        
        <!-- Botão de voltar -->
        <a href="index.php" class="voltar">← Voltar</a>
        
        <!-- Seção de Login -->
        <div class="login-section">
            <p>Já tem cadastro?</p>
            <a href="login.php" class="btn-login">🔐 Login</a>
        </div>
    </form>

</body>
</html>