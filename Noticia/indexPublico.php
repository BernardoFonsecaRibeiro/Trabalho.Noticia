<?php
session_start();
include 'conexao.php';

// Verifica se está logado
$usuario_logado = isset($_SESSION['usuario_id']);
$nome_usuario = $usuario_logado ? $_SESSION['nome'] : null;

// Buscar notícias com nome do autor (de todas as tabelas)
$sql = "SELECT noticias.*, 
        COALESCE(r.nome, a.nome, u.nome) as nome_autor 
        FROM noticias 
        LEFT JOIN reporters r ON noticias.autor = r.id
        LEFT JOIN admins a ON noticias.autor = a.id
        LEFT JOIN usuarios u ON noticias.autor = u.id
        ORDER BY noticias.data DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regionais RS - Notícias</title>
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
        .btn-criar { background: white; color: #b71c1c; }
        .btn-criar:hover { background: #ffebee; transform: translateY(-2px); }
        .btn-usuarios { background: #ff9800; color: white; }
        .btn-usuarios:hover { background: #f57c00; transform: translateY(-2px); }
        .btn-editar { background: #2196F3; color: white; }
        .btn-editar:hover { background: #1976D2; transform: translateY(-2px); }
        .btn-sair { background: rgba(255,255,255,0.2); color: white; }
        .btn-sair:hover { background: rgba(255,255,255,0.3); }
        
        /* CONTEÚDO */
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        
        /* GRID DE NOTÍCIAS */
        .noticias-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        
        /* CARD DE NOTÍCIA */
        .noticia-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .noticia-imagem { 
            width: 100%; 
            height: 200px; 
            object-fit: cover; 
            background: #f5f5f5; 
            display: block; 
        }
        .noticia-conteudo { padding: 25px; }
        .noticia-titulo { color: #b71c1c; font-size: 1.6em; margin-bottom: 10px; }
        .noticia-meta { color: #666; font-size: 0.9em; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .noticia-preview { color: #333; line-height: 1.6; font-size: 0.95em; margin-bottom: 15px; }
        .noticia-texto { color: #333; line-height: 1.8; font-size: 1em; white-space: pre-wrap; }
        
        /* AÇÕES DA NOTÍCIA */
        .noticia-acoes {
            padding: 15px 25px;
            background: #f9f9f9;
            display: flex;
            gap: 10px;
        }
        .btn-editar { background: #2196F3; color: white; }
        .btn-excluir { background: #f44336; color: white; }
        .btn-editar:hover, .btn-excluir:hover { opacity: 0.8; transform: translateY(-2px); }
        
        .sem-noticias { text-align: center; padding: 50px; color: #666; }
        
        /* INFO DO ADMIN */
        .admin-info {
            background: #ffebee;
            padding: 10px 20px;
            border-left: 4px solid #b71c1c;
            margin-bottom: 20px;
            border-radius: 0 5px 5px 0;
        }
        
        .admin-info a {
            color: #b71c1c;
        }
        
        .admin-info a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- 🔴 CABEÇALHO -->
    <div class="header">
        <h1><img src="img/Brasão_do_Rio_Grande_do_Sul.svg.png" alt="Brasão do Rio Grande do Sul" class="header-logo"> Regionais RS</h1>
        <div class="header-botoes">
            <?php if ($usuario_logado): ?>
                <a href="editar_perfil.php" class="btn btn-editar">👤 Editar conta</a>
                <a href="logout.php" class="btn btn-sair">🚪 Sair (<?php echo htmlspecialchars($nome_usuario); ?>)</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-criar">🔐 Fazer Login</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container">
        <!-- Info -->
        <div class="admin-info">
            <?php if ($usuario_logado): ?>
                👤 Bem-vindo, <strong><?php echo htmlspecialchars($nome_usuario); ?></strong> | Visualizando todas as notícias
            <?php else: ?>
                📰 Visualizando todas as notícias | <a href="login.php" style="color: #b71c1c; font-weight: bold; text-decoration: underline;">Faça login para mais funcionalidades</a>
            <?php endif; ?>
        </div>
        
        <!-- Lista de Notícias -->
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="noticias-grid">
                <?php while($noticia = $result->fetch_assoc()): ?>
                    <div class="noticia-card">
                        <?php if(!empty($noticia['imagem'])): ?>
                            <img src="<?php echo htmlspecialchars($noticia['imagem']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>" class="noticia-imagem">
                        <?php endif; ?>
                        
                        <div class="noticia-conteudo">
                            <h2 class="noticia-titulo"><a href="noticia.php?id=<?php echo $noticia['id']; ?>" style="text-decoration: none; color: inherit;"><?php echo htmlspecialchars($noticia['titulo']); ?></a></h2>
                            <div class="noticia-meta">
                                <strong>📅 Publicado:</strong> <?php echo date('d/m/Y H:i', strtotime($noticia['data'])); ?>
                            </div>
                            <div class="noticia-preview">
                                <?php echo htmlspecialchars(substr($noticia['noticia'], 0, 150)) . (strlen($noticia['noticia']) > 150 ? '...' : ''); ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="sem-noticias">
                <p>📭 Nenhuma notícia cadastrada ainda.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>