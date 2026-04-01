<?php
session_start();
session_destroy(); // Destroi toda a sessão
header("Location: login.php");
exit();
?>