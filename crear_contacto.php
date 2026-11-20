<?php
require_once 'auth.php';
require_once 'conexion.php';

$mensaje = "";
$db = (new Conexion())->conectar();

// Obtener la lista de categorías para el <select>
try {
    $stmtCat = $db->query("SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC");
    $categorias = $stmtCat->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $categorias = [];
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $ciudad = trim($_POST['ciudad']);
    $id_categoria = !empty($_POST['id_categoria']) ? intval($_POST['id_categoria']) : null;
    $favorito = isset($_POST['favorito']) ? 1 : 0;
    $id_usuario = $_SESSION['usuario_id'];

    if (!empty($nombre) && !empty($telefono)) {
        if (!preg_match('/^[0-9]{10}$/', $telefono)) {
            $mensaje = "El teléfono debe contener únicamente números y exactamente 10 dígitos.";
        } elseif (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $mensaje = "El formato del correo electrónico no es válido.";
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO contactos (id_usuario, id_categoria, nombre, telefono, correo, ciudad, favorito) 
                                      VALUES (:id_usuario, :id_categoria, :nombre, :telefono, :correo, :ciudad, :favorito)");
                
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                // Si no hay categoría seleccionada, ligamos como NULL en PDO
                if ($id_categoria === null) {
                    $stmt->bindValue(':id_categoria', null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
                }
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':ciudad', $ciudad);
                $stmt->bindParam(':favorito', $favorito, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    header("Location: index.php");
                    exit();
                }
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $mensaje = "Ocurrió un error en el servidor. Por favor intente más tarde.";
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
            <label>Teléfono (10 dígitos):</label>
            <input 
                type="tel" 
                name="telefono" 
                maxlength="10" 
                pattern="[0-9]{10}" 
                inputmode="numeric"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                title="Debe tener exactamente 10 números"
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
        <div>
            <label>Categoría:</label>
            <select name="id_categoria">
                <option value="">-- Sin categoría --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['id_categoria']; ?>">
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <br>
        <div>
            <label>
                <input type="checkbox" name="favorito" value="1">
                ⭐ Marcar como favorito
            </label>
        </div>
        <br>
        <button type="submit">Guardar Contacto</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>