<?php
session_start();
include 'conexao.php';

// Verifica se é reporter ou admin
if (!isset($_SESSION['usuario_id']) || ($_SESSION['tipo'] != 'reporter' && $_SESSION['tipo'] != 'admin')) {
    header("Location: login.php");
    exit();
}

$erro = "";
$sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $noticia = trim($_POST['noticia']);
    $autor = $_SESSION['usuario_id'];
    $tipo_autor = $_SESSION['tipo']; // 'reporter' ou 'admin'
    
    // Upload da imagem
    $imagem = "";
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $pasta_destino = "imagens_noticias/";
        
        // Cria a pasta se não existir
        if (!file_exists($pasta_destino)) {
            mkdir($pasta_destino, 0777, true);
        }
        
        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $nome_imagem = uniqid() . "_" . time() . "." . $extensao;
        $caminho_completo = $pasta_destino . $nome_imagem;
        
        // Valida extensão
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($extensao, $extensoes_permitidas)) {
            // Valida tamanho (5MB max)
            if ($_FILES['imagem']['size'] <= 5242880) {
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_completo)) {
                    $imagem = $caminho_completo;
                } else {
                    $erro = "Erro ao fazer upload da imagem!";
                }
            } else {
                $erro = "Imagem muito grande! Máximo 5MB.";
            }
        } else {
            $erro = "Formato de imagem não permitido! Use JPG, PNG, GIF ou WEBP.";
        }
    }
    
    if (empty($erro)) {
        if (!empty($titulo) && !empty($noticia)) {
            try {
                $stmt = $conn->prepare("INSERT INTO noticias (titulo, noticia, autor, tipo_autor, imagem) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssiss", $titulo, $noticia, $autor, $tipo_autor, $imagem);
                
                if ($stmt->execute()) {
                    $_SESSION['sucesso'] = "Notícia publicada com sucesso!";
                    header("Location: indexReporter.php");
                    exit();
                } else {
                    $erro = "Erro ao cadastrar notícia: " . $conn->error;
                }
            } catch (Exception $e) {
                $erro = "Erro: " . $e->getMessage();
            }
        } else {
            $erro = "Preencha título e conteúdo da notícia!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Notícia - Regionais RS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #b71c1c, #d32f2f);
            color: white;
            padding: 25px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 2.2em;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        h2 {
            color: #b71c1c;
            margin-bottom: 25px;
            border-bottom: 3px solid #b71c1c;
            padding-bottom: 15px;
            font-size: 1.8em;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
            font-size: 1.05em;
        }
        
        input[type="text"],
        textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            font-family: inherit;
            transition: all 0.3s;
        }
        
        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #b71c1c;
            box-shadow: 0 0 0 3px rgba(183, 28, 28, 0.1);
        }
        
        textarea {
            min-height: 350px;
            resize: vertical;
            line-height: 1.6;
        }
        
        input[type="file"] {
            padding: 12px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            width: 100%;
            background: #f9f9f9;
            cursor: pointer;
        }
        
        input[type="file"]:hover {
            border-color: #b71c1c;
            background: #fff5f5;
        }
        
        .botoes {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        button, .btn-voltar {
            flex: 1;
            padding: 16px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
        }
        
        button {
            background: linear-gradient(135deg, #b71c1c, #c62828);
            color: white;
        }
        
        button:hover {
            background: linear-gradient(135deg, #8b0000, #b71c1c);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(183, 28, 28, 0.4);
        }
        
        .btn-voltar {
            background: #757575;
            color: white;
        }
        
        .btn-voltar:hover {
            background: #616161;
            transform: translateY(-2px);
        }
        
        .erro {
            background: linear-gradient(135deg, #ffebee, #ffcdd2);
            color: #b71c1c;
            padding: 18px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 5px solid #b71c1c;
            font-weight: 500;
        }
        
        .ajuda {
            font-size: 0.85em;
            color: #666;
            margin-top: 8px;
            font-style: italic;
        }
        
        .info-autor {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #b71c1c;
        }
        
        .info-autor strong {
            color: #b71c1c;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📰 Regionais RS</h1>
        <p>Painel de Criação de Notícias</p>
    </div>
    
    <div class="container">
        <h2>✍️ Criar Nova Notícia</h2>
        
        <div class="info-autor">
            <strong>👤 Autor:</strong> <?php echo htmlspecialchars($_SESSION['nome']); ?> | 
            <strong>📋 Tipo:</strong> <?php echo $_SESSION['tipo'] == 'admin' ? 'Administrador' : 'Repórter'; ?>
        </div>
        
        <?php if(!empty($erro)) { echo "<div class='erro'>⚠️ $erro</div>"; } ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">📋 Título da Notícia:</label>
                <input type="text" id="titulo" name="titulo" placeholder="Digite um título chamativo e informativo" required maxlength="200">
            </div>
            
            <div class="form-group">
                <label for="imagem">🖼️ Imagem (opcional):</label>
                <input type="file" id="imagem" name="imagem" accept="image/*">
                <p class="ajuda">Formatos: JPG, PNG, GIF, WEBP | Tamanho máximo: 5MB</p>
            </div>
            
            <div class="form-group">
                <label for="noticia">📝 Conteúdo da Notícia:</label>
                <textarea id="noticia" name="noticia" placeholder="Escreva o conteúdo completo da notícia aqui...

Dicas:
- Seja claro e objetivo
- Forneça informações relevantes
- Revise antes de publicar
- Use parágrafos curtos" required></textarea>
            </div>
            
            <div class="botoes">
                <button type="submit">✅ Publicar Notícia</button>
                <a href="indexReporter.php" class="btn-voltar">❌ Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>