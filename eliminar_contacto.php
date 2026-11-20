<?php
// Requiere estar autenticado
require_once 'auth.php';
require_once 'conexion.php';

// Verifica que se reciba el ID del contacto por la URL (GET)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_contacto']) && is_numeric($_POST['id_contacto'])) {
    $id_contacto = intval($_POST['id_contacto']);
    $id_usuario = $_SESSION['usuario_id']; // ID del usuario autenticado

    try {
        $db = (new Conexion())->conectar();

        // Consulta restringida: Se exige que coincidan ambos identificadores
        $stmt = $db->prepare("DELETE FROM contactos WHERE id_contacto = :id_contacto AND id_usuario = :id_usuario");
        $stmt->bindParam(':id_contacto', $id_contacto, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

        $stmt->execute();

    } catch (PDOException $e) {
        error_log($e->getMessage()    );
        die("Ocurrió un error en el servidor. Por favor intente más tarde.");
    }
}

// Redirigir siempre de vuelta al panel principal
header("Location: index.php");
exit();
?>