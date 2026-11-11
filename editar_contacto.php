<?php
require_once 'auth.php';
require_once 'conexion.php';

$id_usuario = $_SESSION['usuario_id'];
$mensaje = "";
$contacto = null;

// Obtiene y valida el ID del contacto desde la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_contacto = intval($_GET['id']);
$db = (new Conexion())->conectar();

// Procesa el formulario cuando se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $ciudad = trim($_POST['ciudad']);

    if (!empty($nombre) && !empty($telefono)) {
        try {
            // Sentencia UPDATE (id_contacto y id_usuario)
            $update = $db->prepare("UPDATE contactos 
                                    SET nombre = :nombre, telefono = :telefono, correo = :correo, ciudad = :ciudad 
                                    WHERE id_contacto = :id_contacto AND id_usuario = :id_usuario");
            $update->bindParam(':nombre', $nombre);
            $update->bindParam(':telefono', $telefono);
            $update->bindParam(':correo', $correo);
            $update->bindParam(':ciudad', $ciudad);
            $update->bindParam(':id_contacto', $id_contacto, PDO::PARAM_INT);
            $update->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

            if ($update->execute()) {
                header("Location: index.php");
                exit();
            }
        } catch (PDOException $e) {
            $mensaje = "Error al actualizar: " . $e->getMessage();
            $contacto = [
                'nombre' => $nombre,
                'telefono' => $telefono,
                'correo' => $correo,
                'ciudad' => $ciudad
            ];
        }
    } else {
        $mensaje = "Nombre y Teléfono son obligatorios.";
        $contacto = [
            'nombre' => $nombre,
            'telefono' => $telefono,
            'correo' => $correo,
            'ciudad' => $ciudad
        ];
    }
}

// Carga los datos actuales de la base de datos si es petición GET inicial
if ($contacto === null) {
    try {
        $stmt = $db->prepare("SELECT * FROM contactos WHERE id_contacto = :id_contacto AND id_usuario = :id_usuario");
        $stmt->bindParam(':id_contacto', $id_contacto, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $contacto = $stmt->fetch();

        if (!$contacto) {
            // Si el contacto no existe o pertenece a otro usuario, redirige
            header("Location: index.php");
            exit();
        }
    } catch (PDOException $e) {
        die("Error al obtener contacto: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Contacto</title>
</head>
<body>
    <h2>Editar Contacto</h2>

    <?php if (!empty($mensaje)): ?>
        <p style="color: red;"><strong><?php echo $mensaje; ?></strong></p>
    <?php endif; ?>

    <form action="editar_contacto.php?id=<?php echo $id_contacto; ?>" method="POST">
        <input type="hidden" name="id_contacto" value="<?php echo $id_contacto; ?>">

        <div>
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($contacto['nombre']); ?>" required>
        </div>
        <br>
        <div>
            <label>Teléfono:</label>
            <input type="text" name="telefono" value="<?php echo htmlspecialchars($contacto['telefono']); ?>" required>
        </div>
        <br>
        <div>
            <label>Correo:</label>
            <input type="email" name="correo" value="<?php echo htmlspecialchars($contacto['correo']); ?>">
        </div>
        <br>
        <div>
            <label>Ciudad:</label>
            <input type="text" name="ciudad" value="<?php echo htmlspecialchars($contacto['ciudad']); ?>">
        </div>
        <br>
        <button type="submit">Actualizar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>
