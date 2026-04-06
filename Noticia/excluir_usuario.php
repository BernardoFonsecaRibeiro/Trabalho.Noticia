<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] != 'admin') {
    header("Location: login.php");
    exit();
}

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!in_array($tipo, ['usuario', 'reporter']) || $id <= 0) {
    header("Location: usuario.php?msg=erro");
    exit();
}

$table = $tipo === 'reporter' ? 'reporters' : 'usuarios';

$stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    header("Location: usuario.php?msg=excluido");
    exit();
} else {
    header("Location: usuario.php?msg=erro");
    exit();
}
