<?php
$servidor = "localhost";
$usuario = "root";
$senha = ""; // No XAMPP padrão a senha é vazia
$banco = "sistema_cadastro";

// Cria a conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>