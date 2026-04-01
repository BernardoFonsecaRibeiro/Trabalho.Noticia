<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['tipo'] != 'reporter' && $_SESSION['tipo'] != 'admin')) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

// Buscar notícia para verificar permissão
$stmt = $conn->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$noticia = $result->fetch_assoc();

if ($noticia) {
    // Verifica se é o autor ou admin
    if ($noticia['autor'] == $_SESSION['usuario_id'] || $_SESSION['tipo'] == 'admin') {
        // Deletar imagem se existir
        if (!empty($noticia['imagem']) && file_exists($noticia['imagem'])) {
            unlink($noticia['imagem']);
        }
        
        // Deletar do banco
        $stmt = $conn->prepare("DELETE FROM noticias WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

header("Location: indexReporter.php?msg=noticia_excluida");
exit();
?>