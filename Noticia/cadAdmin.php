<?php
// === LÓGICA PHP NO TOPO ===
session_start(); // ← IMPORTANTE: Inicia a sessão

if (!isset($_SESSION['admin_access_granted']) || $_SESSION['admin_access_granted'] !== true) {
    header('Location: admin_auth.php');
    exit();
}

include 'conexao.php';

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $cep = $_POST['cep'];
    $rua = $_POST['rua'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $telefone = $_POST['telefone'];

    if (!empty($nome) && !empty($email) && !empty($senha) && !empty($cep) && 
        !empty($rua) && !empty($bairro) && !empty($cidade) && !empty($estado) && !empty($telefone)) {
        
        // Verifica se e-mail já existe na tabela ADMINS
        $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $erro = "Este e-mail já está cadastrado como Admin!";
        } else {
            // Criptografa a senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            // Insere na tabela ADMINS
            $stmt = $conn->prepare("INSERT INTO admins (nome, email, senha, cep, rua, bairro, cidade, estado, telefone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $nome, $email, $senha_hash, $cep, $rua, $bairro, $cidade, $estado, $telefone);
            
            if ($stmt->execute()) {
                // ✅ LOGIN AUTOMÁTICO APÓS CADASTRO
                $_SESSION['usuario_id'] = $conn->insert_id;
                $_SESSION['nome'] = $nome;
                $_SESSION['email'] = $email;
                $_SESSION['tipo'] = 'admin'; // ← Define como admin
                
                // Redireciona para a página do Admin
                header("Location: indexAdmin.php");
                exit();
            } else {
                $erro = "Erro ao cadastrar: " . $conn->error;
            }
        }
        $stmt->close();
    } else {
        $erro = "Por favor, preencha todos os campos obrigatórios!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: linear-gradient(135deg, #b71c1c, #d32f2f); }
        form { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); width: 350px; border-top: 5px solid #8b0000; }
        h2 { text-align: center; color: #b71c1c; margin-bottom: 20px; font-size: 1.5em; }
        input { display: block; width: 100%; margin: 8px 0; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        input:focus { outline: none; border-color: #b71c1c; box-shadow: 0 0 5px rgba(183, 28, 28, 0.3); }
        button { width: 100%; padding: 14px; background: linear-gradient(135deg, #b71c1c, #c62828); color: white; border: none; cursor: pointer; margin-top: 15px; font-weight: bold; border-radius: 5px; font-size: 16px; transition: 0.3s; }
        button:hover { background: linear-gradient(135deg, #8b0000, #b71c1c); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(183, 28, 28, 0.4); }
        .voltar { display: block; margin-top: 15px; text-align: center; color: #666; text-decoration: none; font-size: 0.9em; }
        .voltar:hover { text-decoration: underline; color: #b71c1c; }
        .erro { color: #b71c1c; text-align: center; font-size: 0.9em; margin-bottom: 10px; background: #ffebee; padding: 8px; border-radius: 5px; }
        hr { border: 0; border-top: 1px solid #eee; margin: 20px 0; }
        .section-title { text-align: center; font-size: 0.85em; color: #666; margin-bottom: 10px; }
        .login-section { margin-top: 25px; padding-top: 20px; border-top: 2px dashed #ffcdd2; text-align: center; }
        .login-section p { color: #666; font-size: 0.95em; margin-bottom: 10px; }
        .btn-login { display: inline-block; width: 100%; padding: 12px; background: white; color: #b71c1c; border: 2px solid #b71c1c; border-radius: 5px; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .btn-login:hover { background: #b71c1c; color: white; }
        
        .loading-msg {
            color: #2196F3;
            font-size: 0.85em;
            margin-top: 5px;
            display: none;
        }
    </style>
    <script>
        // Função para buscar dados do CEP via ViaCEP
        function buscarCEP() {
            const cepInput = document.getElementById('cep');
            const cep = cepInput.value.replace(/\D/g, ''); // Remove não-dígitos
            
            // Apenas busca se o CEP tiver 8 dígitos
            if (cep.length !== 8) {
                return;
            }
            
            const loadingMsg = document.getElementById('loading-msg');
            loadingMsg.style.display = 'block';
            
            // Faz a requisição para a API ViaCEP
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    loadingMsg.style.display = 'none';
                    
                    // Verifica se o CEP é válido
                    if (data.erro) {
                        alert('❌ CEP não encontrado!');
                        // Limpa os campos
                        document.getElementById('rua').value = '';
                        document.getElementById('bairro').value = '';
                        document.getElementById('cidade').value = '';
                        document.getElementById('estado').value = '';
                        return;
                    }
                    
                    // Preenche os campos com os dados da API
                    document.getElementById('rua').value = data.logradouro || '';
                    document.getElementById('bairro').value = data.bairro || '';
                    document.getElementById('cidade').value = data.localidade || '';
                    document.getElementById('estado').value = data.uf || '';
                    
                    // Foca no campo de número/complemento se houver
                    document.getElementById('rua').focus();
                })
                .catch(error => {
                    loadingMsg.style.display = 'none';
                    console.error('Erro ao buscar CEP:', error);
                    alert('❌ Erro ao buscar CEP. Tente novamente.');
                });
        }
        
        // Monitora mudanças no campo de CEP
        document.addEventListener('DOMContentLoaded', function() {
            const cepInput = document.getElementById('cep');
            if (cepInput) {
                cepInput.addEventListener('blur', buscarCEP);
                // Também busca ao pressionar Enter
                cepInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        buscarCEP();
                    }
                });
            }
        });
    </script>
</head>
<body>
    <form method="POST" action="">
        <h2>🔐 Cadastro Admin</h2>
        <?php if(!empty($erro)) { echo "<p class='erro'>$erro</p>"; } ?>
        <input type="text" name="nome" placeholder="Nome Completo" required>
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <input type="text" name="telefone" placeholder="Telefone" required>
        <hr>
        <p class="section-title">📍 Dados de Endereço</p>
        <input type="text" name="cep" id="cep" placeholder="CEP" required>
        <div class="loading-msg" id="loading-msg">⏳ Buscando informações...</div>
        <input type="text" name="rua" id="rua" placeholder="Rua" required>
        <input type="text" name="bairro" id="bairro" placeholder="Bairro" required>
        <input type="text" name="cidade" id="cidade" placeholder="Cidade" required>
        <input type="text" name="estado" id="estado" placeholder="Estado (ex: SP)" required>
        <button type="submit">Cadastrar Admin</button>
        <a href="index.php" class="voltar">← Voltar</a>
        <div class="login-section">
            <p>Já tem cadastro?</p>
            <a href="login.php" class="btn-login">🔐 Login</a>
        </div>
    </form>
</body>
</html>