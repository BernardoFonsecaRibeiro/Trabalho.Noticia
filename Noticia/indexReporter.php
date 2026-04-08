<?php
session_start();
include 'conexao.php';

// Verifica se é reporter ou admin
if (!isset($_SESSION['usuario_id']) || ($_SESSION['tipo'] != 'reporter' && $_SESSION['tipo'] != 'admin')) {
    header("Location: login.php");
    exit();
}

// Buscar notícias com nome do autor
$sql = "SELECT noticias.*, reporters.nome as nome_autor 
        FROM noticias 
        INNER JOIN reporters ON noticias.autor = reporters.id 
        ORDER BY noticias.data DESC";

$result = $conn->query($sql);

// Debug: verifica se houve erro na consulta
if (!$result) {
    die("Erro na consulta: " . $conn->error);
}

// Debug: quantas notícias foram encontradas?
// echo "Total de notícias: " . $result->num_rows; exit();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regionais RS - Painel do Repórter</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header {
            background: linear-gradient(135deg, #b71c1c, #d32f2f);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .header h1 { display: inline-flex; align-items: center; gap: 10px; font-size: 2em; }
        .header-logo { width: 42px; height: auto; display: inline-block; }
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
        .btn-criar { background: white; color: #b71c1c; }
        .btn-criar:hover { background: #ffebee; transform: translateY(-2px); }
        .btn-editar { background: #2196F3; color: white; }
        .btn-editar:hover { background: #1976D2; transform: translateY(-2px); }
        .btn-sair { background: rgba(255,255,255,0.2); color: white; }
        .btn-sair:hover { background: rgba(255,255,255,0.3); }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        
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
        .noticia-titulo { color: #b71c1c; font-size: 1.8em; margin-bottom: 10px; }
        .noticia-meta {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .noticia-preview { color: #333; line-height: 1.6; font-size: 0.95em; margin-bottom: 15px; }
        .noticia-texto { color: #333; line-height: 1.8; font-size: 1.1em; }
        .noticia-acoes {
            padding: 15px 25px;
            background: #f9f9f9;
            display: flex;
            gap: 10px;
        }
        .btn-editar { background: #2196F3; color: white; }
        .btn-excluir { background: #f44336; color: white; }
        .sem-noticias {
            text-align: center;
            padding: 50px;
            color: #666;
            font-size: 1.2em;
        }
        .msg-sucesso {
            background: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .reporter-info {
            background: #ffebee;
            padding: 10px 20px;
            border-left: 4px solid #b71c1c;
            margin-bottom: 20px;
            border-radius: 0 5px 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><img src="img/Brasão_do_Rio_Grande_do_Sul.svg.png" alt="Brasão do Rio Grande do Sul" class="header-logo"> Regionais RS</h1>
        <div class="header-botoes">
            <a href="editar_perfil.php" class="btn btn-editar">👤 Editar conta</a>
            <a href="cadNoticias.php" class="btn btn-criar">➕ Criar Notícia</a>
            <a href="logout.php" class="btn btn-sair">🚪 Sair </a>
        </div>
    </div>

    
    <div class="container">
        
    <div class="reporter-info">
            🔐 Painel Reporter | Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'noticia_criada'): ?>
            <div class="msg-sucesso">✅ Notícia criada com sucesso!</div>
        <?php endif; ?>

        
        
        <?php if ($result->num_rows > 0): ?>
            <div class="noticias-grid">
                <?php while($noticia = $result->fetch_assoc()): ?>
                    <div class="noticia-card">
                        <?php if(!empty($noticia['imagem']) && file_exists($noticia['imagem'])): ?>
                            <img src="<?php echo $noticia['imagem']; ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>" class="noticia-imagem">
                        <?php endif; ?>
                        
                        <div class="noticia-conteudo">
                            <h2 class="noticia-titulo"><a href="noticia.php?id=<?php echo $noticia['id']; ?>" style="text-decoration: none; color: inherit;"><?php echo htmlspecialchars($noticia['titulo']); ?></a></h2>
                            <div class="noticia-meta">
                                <strong>👤 Autor:</strong> <?php echo htmlspecialchars($noticia['nome_autor']); ?> | 
                                <strong>📅 Data:</strong> <?php echo date('d/m/Y H:i', strtotime($noticia['data'])); ?>
                            </div>
                            <div class="noticia-preview">
                                <?php echo htmlspecialchars(substr($noticia['noticia'], 0, 150)) . (strlen($noticia['noticia']) > 150 ? '...' : ''); ?>
                            </div>
                        </div>
                        
                        <?php if($noticia['autor'] == $_SESSION['usuario_id'] || $_SESSION['tipo'] == 'admin'): ?>
                            <div class="noticia-acoes">
                                <a href="editar_noticia.php?id=<?php echo $noticia['id']; ?>" class="btn btn-editar">✏️ Editar</a>
                                <a href="excluir_noticia.php?id=<?php echo $noticia['id']; ?>" class="btn btn-excluir" onclick="return confirm('Tem certeza que deseja excluir esta notícia?')">🗑️ Excluir</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="sem-noticias">
                <p>📭 Nenhuma notícia cadastrada ainda.</p>
                <a href="cadNoticias.php" class="btn btn-criar" style="margin-top: 20px;">➕ Criar Primeira Notícia</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>