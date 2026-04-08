<?php
session_start();
include 'conexao.php';

// Verifica se o ID da notícia foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: indexPublico.php");
    exit();
}

$id = (int) $_GET['id'];

// Buscar a notícia com nome do autor
$sql = "SELECT noticias.*, 
        COALESCE(r.nome, a.nome, u.nome) as nome_autor 
        FROM noticias 
        LEFT JOIN reporters r ON noticias.autor = r.id
        LEFT JOIN admins a ON noticias.autor = a.id
        LEFT JOIN usuarios u ON noticias.autor = u.id
        WHERE noticias.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: indexPublico.php");
    exit();
}

$noticia = $result->fetch_assoc();
$stmt->close();

// Verifica se está logado
$usuario_logado = isset($_SESSION['usuario_id']);
$nome_usuario = $usuario_logado ? $_SESSION['nome'] : null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($noticia['titulo']); ?> - Regionais RS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        
        /* 🔴 CABEÇALHO VERMELHO */
        .header {
            background: linear-gradient(135deg, #b71c1c, #d32f2f);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .header h1 { display: inline-flex; align-items: center; gap: 10px; font-size: 1.8em; }
        .header-logo { width: 42px; height: auto; display: inline-block; }
        .header-botoes { display: flex; gap: 10px; }
        
        /* BOTÕES */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            display: inline-block;
            font-size: 14px;
        }
        .btn-voltar { background: white; color: #b71c1c; }
        .btn-voltar:hover { background: #ffebee; transform: translateY(-2px); }
        .btn-login { background: #2196F3; color: white; }
        .btn-login:hover { background: #1976D2; transform: translateY(-2px); }
        .btn-sair { background: rgba(255,255,255,0.2); color: white; }
        .btn-sair:hover { background: rgba(255,255,255,0.3); }
        
        /* CONTEÚDO */
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        
        /* CARD DE NOTÍCIA */
        .noticia-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .noticia-imagem { width: 100%; height: 400px; object-fit: contain; background: #f5f5f5; display: flex; align-items: center; justify-content: center; }
        .noticia-conteudo { padding: 25px; }
        .noticia-titulo { color: #b71c1c; font-size: 2em; margin-bottom: 10px; }
        .noticia-meta { color: #666; font-size: 0.9em; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .noticia-texto { color: #333; line-height: 1.8; font-size: 1.1em; white-space: pre-wrap; }
        
        /* BOTÃO VOLTAR */
        .voltar-container { text-align: center; margin-top: 30px; }
    </style>
</head>
<body>
    <!-- 🔴 CABEÇALHO -->
    <div class="header">
        <h1><img src="img/Brasão_do_Rio_Grande_do_Sul.svg.png" alt="Brasão do Rio Grande do Sul" class="header-logo"> Regionais RS</h1>
        <div class="header-botoes">
            <?php if ($usuario_logado): ?>
                <a href="editar_perfil.php" class="btn btn-voltar">👤 Editar conta</a>
                <a href="logout.php" class="btn btn-sair">🚪 Sair (<?php echo htmlspecialchars($nome_usuario); ?>)</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-login">🔐 Fazer Login</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container">
        <div class="noticia-card">
            <?php if(!empty($noticia['imagem'])): ?>
                <img src="<?php echo htmlspecialchars($noticia['imagem']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>" class="noticia-imagem">
            <?php endif; ?>
            
            <div class="noticia-conteudo">
                <h2 class="noticia-titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h2>
                <div class="noticia-meta">
                    <strong>👤 Autor:</strong> <?php echo htmlspecialchars($noticia['nome_autor'] ?? 'Desconhecido'); ?> | 
                    <strong>📅 Data:</strong> <?php echo date('d/m/Y H:i', strtotime($noticia['data'])); ?>
                </div>
                <div class="noticia-texto">
                    <?php echo nl2br(htmlspecialchars($noticia['noticia'])); ?>
                </div>
            </div>
        </div>
        
        <div class="voltar-container">
            <a href="indexPublico.php" class="btn btn-voltar">← Voltar para Notícias</a>
        </div>
    </div>
</body>
</html>