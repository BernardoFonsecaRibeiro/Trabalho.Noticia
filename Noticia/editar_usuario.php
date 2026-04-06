<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] != 'admin') {
    header("Location: login.php");
    exit();
}

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : (isset($_POST['tipo']) ? $_POST['tipo'] : '');
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if (!in_array($tipo, ['usuario', 'reporter']) || $id <= 0) {
    header("Location: usuario.php?msg=erro");
    exit();
}

$table = $tipo === 'reporter' ? 'reporters' : 'usuarios';
$erro = '';

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
        $stmt = $conn->prepare("SELECT id FROM $table WHERE email = ? AND id <> ?");
        $stmt->bind_param('si', $email, $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $stmt->close();

            if ($senha !== '') {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                if ($tipo === 'reporter') {
                    $stmt = $conn->prepare("UPDATE $table SET nome = ?, email = ?, senha = ?, cep = ?, rua = ?, bairro = ?, cidade = ?, estado = ?, telefone = ? WHERE id = ?");
                    $stmt->bind_param('sssssssssi', $nome, $email, $senha_hash, $cep, $rua, $bairro, $cidade, $estado, $telefone, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE $table SET nome = ?, email = ?, senha = ? WHERE id = ?");
                    $stmt->bind_param('sssi', $nome, $email, $senha_hash, $id);
                }
            } else {
                if ($tipo === 'reporter') {
                    $stmt = $conn->prepare("UPDATE $table SET nome = ?, email = ?, cep = ?, rua = ?, bairro = ?, cidade = ?, estado = ?, telefone = ? WHERE id = ?");
                    $stmt->bind_param('ssssssssi', $nome, $email, $cep, $rua, $bairro, $cidade, $estado, $telefone, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE $table SET nome = ?, email = ? WHERE id = ?");
                    $stmt->bind_param('ssi', $nome, $email, $id);
                }
            }

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: usuario.php?msg=atualizado");
                exit();
            } else {
                $erro = 'Erro ao atualizar: ' . $conn->error;
            }
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$registro = $result->fetch_assoc();
$stmt->close();

if (!$registro) {
    header("Location: usuario.php?msg=erro");
    exit();
}

$title = $tipo === 'reporter' ? 'Editar Repórter' : 'Editar Leitor';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f0f0; color: #333; }
        .container { max-width: 600px; margin: 40px auto; padding: 20px; background: white; border-radius: 12px; box-shadow: 0 16px 40px rgba(0,0,0,0.08); }
        h1 { margin-bottom: 20px; color: #b71c1c; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input { width: 100%; padding: 12px 14px; border-radius: 8px; border: 1px solid #ccc; font-size: 14px; }
        input:focus { outline: none; border-color: #b71c1c; box-shadow: 0 0 0 3px rgba(183,28,28,0.1); }
        button { width: 100%; padding: 14px; border: none; border-radius: 8px; background: linear-gradient(135deg, #b71c1c, #d32f2f); color: white; font-size: 16px; font-weight: bold; cursor: pointer; }
        .actions { display: flex; gap: 10px; margin-top: 20px; }
        .btn-secondary { flex: 1; text-align: center; display: inline-block; text-decoration: none; padding: 12px 14px; border-radius: 8px; background: #f5f5f5; color: #333; }
        .error { margin-bottom: 18px; padding: 12px 14px; background: #ffebee; border: 1px solid #ef9a9a; color: #c62828; border-radius: 8px; }
        .help { font-size: 0.95rem; color: #555; margin-top: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $title; ?></h1>
        <?php if ($erro): ?>
            <div class="error"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="post" action="editar_usuario.php">
            <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($tipo); ?>">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($registro['nome']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($registro['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha (deixe em branco para manter)</label>
                <input type="password" id="senha" name="senha" placeholder="Nova senha opcional">
                <div class="help">Preencha apenas se quiser alterar a senha.</div>
            </div>

            <?php if ($tipo === 'reporter'): ?>
                <div class="form-group">
                    <label for="cep">CEP</label>
                    <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($registro['cep']); ?>">
                </div>

                <div class="form-group">
                    <label for="rua">Rua</label>
                    <input type="text" id="rua" name="rua" value="<?php echo htmlspecialchars($registro['rua']); ?>">
                </div>

                <div class="form-group">
                    <label for="bairro">Bairro</label>
                    <input type="text" id="bairro" name="bairro" value="<?php echo htmlspecialchars($registro['bairro']); ?>">
                </div>

                <div class="form-group">
                    <label for="cidade">Cidade</label>
                    <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($registro['cidade']); ?>">
                </div>

                <div class="form-group">
                    <label for="estado">Estado (UF)</label>
                    <input type="text" id="estado" name="estado" maxlength="2" value="<?php echo htmlspecialchars($registro['estado']); ?>">
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($registro['telefone']); ?>">
                </div>
            <?php endif; ?>

            <button type="submit">Salvar alterações</button>
        </form>

        <div class="actions">
            <a href="usuario.php" class="btn-secondary">← Voltar</a>
        </div>
    </div>
</body>
</html>
