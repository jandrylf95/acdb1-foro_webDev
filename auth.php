<?php
// Middleware de protección
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Comprobar si existe la variable de sesión
if (!isset($_SESSION['usuario_id'])) {
    // Si no ha iniciado sesión, redirige a login
    header("Location: login.php");
    exit();
}
?>