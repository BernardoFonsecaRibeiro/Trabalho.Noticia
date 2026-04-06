<?php
session_start();
include 'conexao.php';

// Verifica se está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$tipo = $_SESSION['tipo'];
$tabela = ($tipo === 'reporter') ? 'reporters' : 'usuarios';

// Excluir conta
$stmt = $conn->prepare("DELETE FROM $tabela WHERE id = ?");
$stmt->bind_param('i', $_SESSION['usuario_id']);

if ($stmt->execute()) {
    // Limpar sessão
    session_destroy();
    header("Location: index.php?msg=conta_excluida");
    exit();
} else {
    header("Location: editar_perfil.php?erro=exclusao_falhou");
    exit();
}
?>