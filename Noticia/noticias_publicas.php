<?php
session_start();
include 'conexao.php';

// Verifica se está logado apenas para mostrar o nome se estiver
$usuario_logado = isset($_SESSION['usuario_id']);
$nome_usuario = $usuario_logado ? $_SESSION['nome'] : null;

// Busca as 5 notícias mais recentes
$sql = "SELECT noticias.*, 
        COALESCE(r.nome, a.nome, u.nome) as nome_autor 
        FROM noticias 
        LEFT JOIN reporters r ON noticias.autor = r.id 
        LEFT JOIN admins a ON noticias.autor = a.id 
        LEFT JOIN usuarios u ON noticias.autor = u.id 
        ORDER BY noticias.data DESC 
        LIMIT 5";
$result = $conn->query($sql);

$totalResult = $conn->query("SELECT COUNT(*) as total FROM noticias");
$totalNoticias = 0;
if ($totalResult) {
    $totalNoticias = (int) $totalResult->fetch_assoc()['total'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regionais RS - Notícias Públicas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header { background: linear-gradient(135deg, #b71c1c, #d32f2f); color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        .header h1 { display: inline-flex; align-items: center; gap: 10px; font-size: 1.8em; }
        .header-logo { width: 42px; height: auto; }
        .header-botoes { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: bold; transition: 0.3s; display: inline-block; font-size: 14px; }
        .btn-criar { background: white; color: #b71c1c; }
        .btn-criar:hover { background: #ffebee; transform: translateY(-2px); }
        .btn-login { background: #2196F3; color: white; }
        .btn-login:hover { background: #1976D2; transform: translateY(-2px); }
        .btn-sair { background: rgba(255,255,255,0.2); color: white; }
        .btn-sair:hover { background: rgba(255,255,255,0.3); }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .notice { background: #fff3e0; color: #e65100; padding: 18px 22px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #ffcc80; }
        
        /* GRID DE NOTÍCIAS */
        .noticias-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        
        .noticia-card { 
            background: white; 
            border-radius: 10px; 
            box-shadow: 0 3px 15px rgba(0,0,0,0.1); 
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .noticia-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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
        .info-rodape { background: #f0f0f0; padding: 20px; border-radius: 10px; text-align: center; margin-bottom: 30px; }
        .info-rodape p { margin-bottom: 10px; color: #333; }
        .info-actions { display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; }
        .info-actions a { min-width: 180px; }
        .sem-noticias { text-align: center; padding: 50px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1><img src="img/Brasão_do_Rio_Grande_do_Sul.svg.png" alt="Brasão do Rio Grande do Sul" class="header-logo"> Regionais RS</h1>
        <div class="header-botoes">
            <?php if ($usuario_logado): ?>
                <span style="padding: 10px 20px; border-radius: 5px; background: rgba(255,255,255,0.2);">👤 <?php echo htmlspecialchars($nome_usuario); ?></span>
                <a href="logout.php" class="btn btn-sair">🚪 Sair</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-login">🔐 Login</a>
                <a href="cadPublico.php" class="btn btn-criar">👤 Criar Conta</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <div class="notice">
            Você está vendo as **5 últimas notícias** públicas. Para acessar mais notícias, <strong>faça login</strong> ou <strong>crie uma conta</strong>.
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="noticias-grid">
                <?php while ($noticia = $result->fetch_assoc()): ?>
                    <div class="noticia-card">
                        <?php if (!empty($noticia['imagem'])): ?>
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
                <p>📭 Nenhuma notícia disponível no momento.</p>
            </div>
        <?php endif; ?>

        <?php if ($totalNoticias > 5): ?>
            <div class="info-rodape">
                <p>Existem mais notícias disponíveis.</p>
                <div class="info-actions">
                    <a href="login.php" class="btn btn-login">🔐 Login para ver mais</a>
                    <a href="cadPublico.php" class="btn btn-criar">👤 Criar conta</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
