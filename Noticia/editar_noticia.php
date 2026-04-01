<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['tipo'] != 'reporter' && $_SESSION['tipo'] != 'admin')) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$erro = "";

// Buscar notícia
$stmt = $conn->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$noticia = $result->fetch_assoc();

// Verifica se é o autor ou admin
if ($noticia['autor'] != $_SESSION['usuario_id'] && $_SESSION['tipo'] != 'admin') {
    header("Location: indexReporter.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $conteudo = $_POST['noticia'];
    
    // Verificar se há nova imagem
    $imagem = $noticia['imagem'];
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        // Deletar imagem antiga
        if (!empty($noticia['imagem']) && file_exists($noticia['imagem'])) {
            unlink($noticia['imagem']);
        }
        
        $pasta_destino = "imagens_noticias/";
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $nome_imagem = uniqid() . "." . $extensao;
        $caminho_completo = $pasta_destino . $nome_imagem;
        
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_completo)) {
            $imagem = $caminho_completo;
        }
    }
    
    $stmt = $conn->prepare("UPDATE noticias SET titulo = ?, noticia = ?, imagem = ? WHERE id = ?");
    $stmt->bind_param("sssi", $titulo, $conteudo, $imagem, $id);
    
    if ($stmt->execute()) {
        header("Location: indexReporter.php?msg=noticia_editada");
        exit();
    } else {
        $erro = "Erro ao editar: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Notícia</title>
    <style>
        /* Use o mesmo CSS do cadNoticias.php */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header { background: linear-gradient(135deg, #b71c1c, #d32f2f); color: white; padding: 20px; text-align: center; }
        .container { max-width: 900px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; }
        h2 { color: #b71c1c; margin-bottom: 20px; border-bottom: 3px solid #b71c1c; padding-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 5px; font-size: 16px; }
        textarea { min-height: 300px; }
        input[type="file"] { padding: 10px; border: 2px dashed #ddd; border-radius: 5px; width: 100%; }
        .botoes { display: flex; gap: 15px; margin-top: 30px; }
        button, .btn-voltar { flex: 1; padding: 15px; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; text-decoration: none; text-align: center; }
        button { background: linear-gradient(135deg, #b71c1c, #c62828); color: white; }
        .btn-voltar { background: #757575; color: white; }
        .erro { background: #ffebee; color: #b71c1c; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .imagem-atual { margin: 10px 0; }
        .imagem-atual img { max-width: 300px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✏️ Editar Notícia</h1>
    </div>
    
    <div class="container">
        <?php if(!empty($erro)) { echo "<div class='erro'>$erro</div>"; } ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Título:</label>
                <input type="text" name="titulo" value="<?php echo htmlspecialchars($noticia['titulo']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Imagem atual:</label>
                <?php if(!empty($noticia['imagem'])): ?>
                    <div class="imagem-atual">
                        <img src="<?php echo $noticia['imagem']; ?>" alt="Imagem atual">
                    </div>
                <?php endif; ?>
                <input type="file" name="imagem" accept="image/*">
                <small>Deixe em branco para manter a imagem atual</small>
            </div>
            
            <div class="form-group">
                <label>Conteúdo:</label>
                <textarea name="noticia" required><?php echo htmlspecialchars($noticia['noticia']); ?></textarea>
            </div>
            
            <div class="botoes">
                <button type="submit">💾 Salvar Alterações</button>
                <a href="indexReporter.php" class="btn-voltar">❌ Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>