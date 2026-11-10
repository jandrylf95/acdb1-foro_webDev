<?php
require_once 'auth.php';
require_once 'conexion.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $ciudad = trim($_POST['ciudad']);
    $id_usuario = $_SESSION['usuario_id']; // Vincula a la sesión activa

    if (!empty($nombre) && !empty($telefono)) {
        if (!preg_match('/^[0-9]{1,10}$/', $telefono)) {
            $mensaje = "El teléfono debe contener únicamente números y un máximo de 10 dígitos.";
        } else {
            try {
                $db = (new Conexion())->conectar();
                $stmt = $db->prepare("INSERT INTO contactos (id_usuario, nombre, telefono, correo, ciudad) 
                                      VALUES (:id_usuario, :nombre, :telefono, :correo, :ciudad)");
                
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':ciudad', $ciudad);

                if ($stmt->execute()) {
                    header("Location: index.php");
                    exit();
                }
            } catch (PDOException $e) {
                $mensaje = "Error al guardar el contacto: " . $e->getMessage();
            }
        }
    } else {
        $mensaje = "El nombre y el teléfono son campos obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Contacto - Agenda</title>
</head>
<body>
    <h2>Agregar Nuevo Contacto</h2>

    <?php if (!empty($mensaje)): ?>
        <p style="color: red;"><strong><?php echo $mensaje; ?></strong></p>
    <?php endif; ?>

    <form action="crear_contacto.php" method="POST">
        <div>
            <label>Nombre y Apellido:</label>
            <input type="text" name="nombre" required>
        </div>
        <br>
        <div>
            <label>Teléfono:</label>
            <input 
                type="tel" 
                name="telefono" 
                maxlength="10" 
                pattern="[0-9]{1,10}" 
                inputmode="numeric"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                title="Solo se permiten hasta 10 números"
                required>
        </div>
        <br>
        <div>
            <label>Correo Electrónico:</label>
            <input type="email" name="correo">
        </div>
        <br>
        <div>
            <label>Ciudad:</label>
            <input type="text" name="ciudad">
        </div>
        <br>
        <button type="submit">Guardar Contacto</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>