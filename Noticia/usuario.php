<?php
session_start();
include 'conexao.php';

// 🔐 Verifica se é ADMIN
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] != 'admin') {
    header("Location: login.php");
    exit();
}

$usuarios = $conn->query("SELECT id, nome, email, data_cadastro FROM usuarios ORDER BY nome ASC");
$reporters = $conn->query("SELECT id, nome, email, cep, rua, bairro, cidade, estado, telefone, data_cadastro FROM reporters ORDER BY nome ASC");

$msg = null;
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'atualizado':
            $msg = '✅ Registro atualizado com sucesso.';
            break;
        case 'excluido':
            $msg = '✅ Registro excluído com sucesso.';
            break;
        case 'erro':
            $msg = '⚠️ Ocorreu um erro ao processar sua solicitação.';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Contas - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f2f2f2; color: #333; }
        .header { background: linear-gradient(135deg, #b71c1c, #d32f2f); color: white; padding: 18px 28px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.8rem; }
        .header a { text-decoration: none; }
        .btn { display: inline-block; padding: 10px 18px; border-radius: 7px; text-decoration: none; font-weight: bold; transition: 0.2s; }
        .btn-voltar { background: rgba(255,255,255,0.2); color: white; }
        .btn-sair { background: rgba(255,255,255,0.15); color: white; }
        .container { max-width: 1200px; margin: 28px auto; padding: 0 20px; }
        .notice { padding: 14px 18px; border-radius: 8px; margin-bottom: 18px; background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .section { margin-bottom: 30px; }
        .section h2 { margin-bottom: 14px; color: #b71c1c; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 10px 24px rgba(0,0,0,0.08); }
        th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f7f7f7; color: #333; }
        tr:hover { background: #fafafa; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-editar { background: #1976d2; color: white; }
        .btn-excluir { background: #d32f2f; color: white; }
        .btn-editar:hover, .btn-excluir:hover, .btn-voltar:hover, .btn-sair:hover { opacity: 0.95; }
        .empty { padding: 20px; text-align: center; color: #666; }
        .small { font-size: 0.95rem; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h1>👥 Gerenciador de Contas</h1>
        <div>
            <a href="indexAdmin.php" class="btn btn-voltar">⏪ Voltar ao Painel</a>
            <a href="logout.php" class="btn btn-sair">🚪 Sair</a>
        </div>
    </div>
    <div class="container">
        <?php if ($msg): ?>
            <div class="notice"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>

        <div class="section">
            <h2>Leitores</h2>
            <?php if ($usuarios && $usuarios->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($usuario['data_cadastro'])); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="editar_usuario.php?tipo=usuario&id=<?php echo $usuario['id']; ?>" class="btn btn-editar">✏️ Editar</a>
                                        <a href="excluir_usuario.php?tipo=usuario&id=<?php echo $usuario['id']; ?>" class="btn btn-excluir" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">🗑️ Excluir</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">Nenhum leitor cadastrado ainda.</div>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Repórteres</h2>
            <?php if ($reporters && $reporters->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Cidade / Estado</th>
                            <th>Telefone</th>
                            <th>Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($reporter = $reporters->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reporter['nome']); ?></td>
                                <td><?php echo htmlspecialchars($reporter['email']); ?></td>
                                <td><?php echo htmlspecialchars($reporter['cidade'] . ' / ' . $reporter['estado']); ?></td>
                                <td><?php echo htmlspecialchars($reporter['telefone']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($reporter['data_cadastro'])); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="editar_usuario.php?tipo=reporter&id=<?php echo $reporter['id']; ?>" class="btn btn-editar">✏️ Editar</a>
                                        <a href="excluir_usuario.php?tipo=reporter&id=<?php echo $reporter['id']; ?>" class="btn btn-excluir" onclick="return confirm('Tem certeza que deseja excluir este repórter?');">🗑️ Excluir</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">Nenhum repórter cadastrado ainda.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
