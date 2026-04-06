<?php
session_start();
include 'conexao.php';

// 🔐 Verifica se é ADMIN
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] != 'admin') {
    header("Location: login.php");
    exit();
}

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
    <title>Regionais RS - Painel Admin</title>
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
        .btn-sair { background: rgba(255,255,255,0.2); color: white; }
        .btn-sair:hover { background: rgba(255,255,255,0.3); }
        
        /* CONTEÚDO */
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        
        /* CARD DE NOTÍCIA */
        .noticia-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        .noticia-imagem { width: 100%; height: 300px; object-fit: contain; background: #f5f5f5; display: flex; align-items: center; justify-content: center; }
        .noticia-conteudo { padding: 25px; }
        .noticia-titulo { color: #b71c1c; font-size: 1.6em; margin-bottom: 10px; }
        .noticia-meta { color: #666; font-size: 0.9em; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
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
    </style>
</head>
<body>
    <!-- 🔴 CABEÇALHO -->
    <div class="header">
        <h1><img src="img/Brasão_do_Rio_Grande_do_Sul.svg.png" alt="Brasão do Rio Grande do Sul" class="header-logo"> Regionais RS</h1>
        <div class="header-botoes">
            <a href="usuario.php" class="btn btn-usuarios">👥 Usuários</a>
            <a href="logout.php" class="btn btn-sair">🚪 Sair (<?php echo htmlspecialchars($_SESSION['nome']); ?>)</a>
        </div>
    </div>
    
    <div class="container">
        <!-- Info do Admin logado -->
        <div class="admin-info">
            🔐 Painel Administrativo | Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong>
        </div>
        
        <!-- Lista de Notícias -->
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($noticia = $result->fetch_assoc()): ?>
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
                            <?php echo htmlspecialchars($noticia['noticia']); ?>
                        </div>
                    </div>
                    
                    <!-- Botões de Editar/Excluir (Admin pode tudo) -->
                    <div class="noticia-acoes">
                        <a href="editar_noticia.php?id=<?php echo $noticia['id']; ?>" class="btn btn-editar">✏️ Editar</a>
                        <a href="excluir_noticia.php?id=<?php echo $noticia['id']; ?>" class="btn btn-excluir" onclick="return confirm('Tem certeza que deseja EXCLUIR esta notícia?')">🗑️ Excluir</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="sem-noticias">
                <p>📭 Nenhuma notícia cadastrada ainda.</p>
                <p>🔒 Apenas repórteres podem criar notícias.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>