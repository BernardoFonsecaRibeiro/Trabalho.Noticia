<?php
session_start();
include 'conexao.php';

// Verifica se está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$tipo = $_SESSION['tipo'];
$tabela = ($tipo === 'reporter') ? 'reporters' : 'usuarios';
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $cep = trim($_POST['cep'] ?? '');
    $rua = trim($_POST['rua'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if ($nome === '' || $email === '') {
        $erro = 'Nome e e-mail são obrigatórios.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM $tabela WHERE email = ? AND id <> ?");
        $stmt->bind_param('si', $email, $_SESSION['usuario_id']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $stmt->close();

            if ($senha !== '') {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                if ($tipo === 'reporter') {
                    $stmt = $conn->prepare("UPDATE $tabela SET nome = ?, email = ?, senha = ?, cep = ?, rua = ?, bairro = ?, cidade = ?, estado = ?, telefone = ? WHERE id = ?");
                    $stmt->bind_param('sssssssssi', $nome, $email, $senha_hash, $cep, $rua, $bairro, $cidade, $estado, $telefone, $_SESSION['usuario_id']);
                } else {
                    $stmt = $conn->prepare("UPDATE $tabela SET nome = ?, email = ?, senha = ? WHERE id = ?");
                    $stmt->bind_param('sssi', $nome, $email, $senha_hash, $_SESSION['usuario_id']);
                }
            } else {
                if ($tipo === 'reporter') {
                    $stmt = $conn->prepare("UPDATE $tabela SET nome = ?, email = ?, cep = ?, rua = ?, bairro = ?, cidade = ?, estado = ?, telefone = ? WHERE id = ?");
                    $stmt->bind_param('ssssssssi', $nome, $email, $cep, $rua, $bairro, $cidade, $estado, $telefone, $_SESSION['usuario_id']);
                } else {
                    $stmt = $conn->prepare("UPDATE $tabela SET nome = ?, email = ? WHERE id = ?");
                    $stmt->bind_param('ssi', $nome, $email, $_SESSION['usuario_id']);
                }
            }

            if ($stmt->execute()) {
                $_SESSION['nome'] = $nome;
                $_SESSION['email'] = $email;
                $sucesso = 'Perfil atualizado com sucesso!';
            } else {
                $erro = 'Erro ao atualizar perfil: ' . $conn->error;
            }
        }
    }
}

// Buscar dados atuais
$stmt = $conn->prepare("SELECT * FROM $tabela WHERE id = ?");
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

$title = ($tipo === 'reporter') ? 'Editar Perfil - Repórter' : 'Editar Perfil - Leitor';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Regionais RS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; color: #333; }

        .header {
            background: linear-gradient(135deg, #b71c1c, #d32f2f);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .header h1 { font-size: 1.8em; }
        .header-botoes { display: flex; gap: 10px; }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            display: inline-block;
        }

        .btn-voltar { background: rgba(255,255,255,0.2); color: white; }
        .btn-voltar:hover { background: rgba(255,255,255,0.3); }

        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }

        h2 { color: #b71c1c; margin-bottom: 20px; text-align: center; }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        input:focus { outline: none; border-color: #b71c1c; }

        .buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-primary {
            flex: 1;
            background: linear-gradient(135deg, #b71c1c, #d32f2f);
            color: white;
            text-align: center;
        }

        .btn-secondary {
            flex: 1;
            background: #757575;
            color: white;
            text-align: center;
        }

        .btn-primary:hover, .btn-secondary:hover { opacity: 0.9; }

        .erro { background: #ffebee; color: #b71c1c; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .sucesso { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px; }

        .delete-section {
            margin-top: 40px;
            padding: 20px;
            background: #ffebee;
            border-radius: 8px;
            border: 1px solid #ffcdd2;
        }

        .delete-section h3 { color: #b71c1c; margin-bottom: 15px; }
        .delete-section p { margin-bottom: 15px; color: #666; }

        .btn-delete {
            background: #f44336;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-delete:hover { background: #d32f2f; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📰 Regionais RS</h1>
        <div class="header-botoes">
            <a href="<?php echo ($tipo === 'reporter') ? 'indexReporter.php' : 'indexPublico.php'; ?>" class="btn btn-voltar">← Voltar</a>
        </div>
    </div>

    <div class="container">
        <h2>Editar Meu Perfil</h2>

        <?php if ($erro): ?>
            <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div class="sucesso"><?php echo htmlspecialchars($sucesso); ?></div>
        <?php endif; ?>

        <form method="post" action="editar_perfil.php">
            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="senha">Nova Senha (deixe em branco para manter)</label>
                <input type="password" id="senha" name="senha" placeholder="Digite apenas se quiser alterar">
            </div>

            <?php if ($tipo === 'reporter'): ?>
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone']); ?>">
                </div>

                <div class="form-group">
                    <label for="cep">CEP</label>
                    <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($usuario['cep']); ?>">
                </div>

                <div class="form-group">
                    <label for="rua">Rua</label>
                    <input type="text" id="rua" name="rua" value="<?php echo htmlspecialchars($usuario['rua']); ?>">
                </div>

                <div class="form-group">
                    <label for="bairro">Bairro</label>
                    <input type="text" id="bairro" name="bairro" value="<?php echo htmlspecialchars($usuario['bairro']); ?>">
                </div>

                <div class="form-group">
                    <label for="cidade">Cidade</label>
                    <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($usuario['cidade']); ?>">
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <input type="text" id="estado" name="estado" value="<?php echo htmlspecialchars($usuario['estado']); ?>" maxlength="2">
                </div>
            <?php endif; ?>

            <div class="buttons">
                <button type="submit" class="btn-primary">💾 Salvar Alterações</button>
                <a href="<?php echo ($tipo === 'reporter') ? 'indexReporter.php' : 'indexPublico.php'; ?>" class="btn-secondary">❌ Cancelar</a>
            </div>
        </form>

        <div class="delete-section">
            <h3>⚠️ Zona de Perigo</h3>
            <p>Ao excluir sua conta, todos os seus dados serão removidos permanentemente e não poderão ser recuperados.</p>
            <button class="btn-delete" onclick="if(confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.')) { window.location.href='excluir_perfil.php'; }">🗑️ Excluir Minha Conta</button>
        </div>
    </div>
</body>
</html>