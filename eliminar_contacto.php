<?php
// Requiere estar autenticado
require_once 'auth.php';
require_once 'conexion.php';

// Verifica que se reciba el ID del contacto por la URL (GET)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_contacto = intval($_GET['id']);
    $id_usuario = $_SESSION['usuario_id']; // ID del usuario autenticado

    try {
        $db = (new Conexion())->conectar();

        // Consulta restringida: Se exige que coincidan AMBOS identificadores
        $stmt = $db->prepare("DELETE FROM contactos WHERE id_contacto = :id_contacto AND id_usuario = :id_usuario");
        $stmt->bindParam(':id_contacto', $id_contacto, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

        $stmt->execute();

    } catch (PDOException $e) {
        die("Error al eliminar el contacto: " . $e->getMessage());
    }
}

// Redirigir siempre de vuelta al panel principal
header("Location: index.php");
exit();
?>